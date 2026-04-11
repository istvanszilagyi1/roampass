import cv2
import pytesseract
import sys
import json
import re
import numpy as np

# --- KONFIGURÁCIÓ ---
pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'


def print_debug(msg):
    sys.stderr.write(f"[DEBUG] {msg}\n")
    sys.stderr.flush()


def resize_to_limit(image, max_width=1000):
    h, w = image.shape[:2]
    if w <= max_width:
        return image
    ratio = max_width / float(w)
    return cv2.resize(image, (max_width, int(h * ratio)), interpolation=cv2.INTER_AREA)


def get_target_hsv(target_hex):
    """
    A cél szín HSV értékének kiszámítása.
    """
    hex_color = target_hex.lstrip('#')
    rgb = tuple(int(hex_color[i:i + 2], 16) for i in (0, 2, 4))
    tmp_img = np.uint8([[rgb]])
    target_hsv = cv2.cvtColor(tmp_img, cv2.COLOR_RGB2HSV)[0][0]
    return target_hsv


def get_hsv_ranges(target_hex, h_tol=15, s_min=50, v_min=50):
    """
    Mérsékelt HSV határok számítása a pontosabb színfelismeréshez.
    """
    target_hsv = get_target_hsv(target_hex)

    target_h = int(target_hsv[0])
    target_s = int(target_hsv[1])
    target_v = int(target_hsv[2])

    # Szűkebb tartományok a cél szín körül
    low_h = max(0, target_h - h_tol)
    high_h = min(179, target_h + h_tol)

    # Telítettség és érték dinamikus alsó határai
    # A cél szín s és v értékének legalább 40%-a legyen az alsó határ
    s_lower = max(s_min, int(target_s * 0.4))
    v_lower = max(v_min, int(target_v * 0.4))

    lower = np.array([low_h, s_lower, v_lower])
    upper = np.array([high_h, 255, 255])

    # Piros szín esetén (H=0 vagy H=179 körül) külön kezelés
    lower2, upper2 = None, None
    if target_h < h_tol or target_h > (179 - h_tol):
        if target_h < h_tol:
            # Piros alsó tartományban
            lower1 = np.array([0, s_lower, v_lower])
            upper1 = np.array([target_h + h_tol, 255, 255])
            lower2 = np.array([179 - (h_tol - target_h), s_lower, v_lower])
            upper2 = np.array([179, 255, 255])
            return lower1, upper1, lower2, upper2
        else:
            # Piros felső tartományban
            lower1 = np.array([target_h - h_tol, s_lower, v_lower])
            upper1 = np.array([179, 255, 255])
            lower2 = np.array([0, s_lower, v_lower])
            upper2 = np.array([h_tol - (179 - target_h), 255, 255])
            return lower1, upper1, lower2, upper2

    return lower, upper, lower2, upper2


def verify_color_accuracy(image, contour, target_hex, threshold=0.3):
    """
    Ellenőrzi, hogy a talált kontúr valóban a cél színhez közeli-e.
    """
    mask = np.zeros(image.shape[:2], dtype=np.uint8)
    cv2.drawContours(mask, [contour], -1, 255, -1)

    # Kivágjuk a kontúr területét
    hsv = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)

    # Csak a kontúrban lévő pixeleket vesszük figyelembe
    pixels = hsv[mask > 0]
    if len(pixels) == 0:
        return False

    # Cél HSV értékek
    target_hsv = get_target_hsv(target_hex)

    # Szűkebb tolerancia a megerősítéshez
    h_tol = 12
    s_min = 40
    v_min = 40

    # Dinamikus alsó határok a cél szín alapján
    s_lower = max(s_min, int(target_hsv[1] * 0.35))
    v_lower = max(v_min, int(target_hsv[2] * 0.35))

    lower = np.array([max(0, target_hsv[0] - h_tol), s_lower, v_lower])
    upper = np.array([min(179, target_hsv[0] + h_tol), 255, 255])

    # Piros szín külön kezelése a megerősítésnél is
    valid_pixels = 0
    if target_hsv[0] < h_tol or target_hsv[0] > (179 - h_tol):
        # Piros szín esetén két tartományt kell ellenőrizni
        lower1 = np.array([0, s_lower, v_lower])
        upper1 = np.array([target_hsv[0] + h_tol, 255, 255])
        lower2 = np.array([179 - (h_tol - target_hsv[0]), s_lower, v_lower])
        upper2 = np.array([179, 255, 255])

        valid1 = np.sum(np.all((pixels >= lower1) & (pixels <= upper1), axis=1))
        valid2 = np.sum(np.all((pixels >= lower2) & (pixels <= upper2), axis=1))
        valid_pixels = valid1 + valid2
    else:
        valid_pixels = np.sum(np.all((pixels >= lower) & (pixels <= upper), axis=1))

    ratio = valid_pixels / len(pixels)
    return ratio > threshold


def find_color_blobs(image, target_hex, min_area=200):
    """
    Megkeresi a megadott színű foltokat a képen, mérsékelt toleranciával.
    """
    # Kép simítása a zajok csökkentésére
    blurred = cv2.GaussianBlur(image, (5, 5), 0)
    hsv = cv2.cvtColor(blurred, cv2.COLOR_BGR2HSV)

    lower1, upper1, lower2, upper2 = get_hsv_ranges(target_hex)

    mask1 = cv2.inRange(hsv, lower1, upper1)
    if lower2 is not None:
        mask2 = cv2.inRange(hsv, lower2, upper2)
        mask = cv2.bitwise_or(mask1, mask2)
    else:
        mask = mask1

    # Finomabb morfológiai műveletek a zajok szűrésére
    kernel = np.ones((3, 3), np.uint8)
    mask = cv2.morphologyEx(mask, cv2.MORPH_OPEN, kernel)
    mask = cv2.morphologyEx(mask, cv2.MORPH_CLOSE, kernel)

    # Erodálás a kisebb zajok eltávolítására
    mask = cv2.erode(mask, kernel, iterations=1)
    mask = cv2.dilate(mask, kernel, iterations=1)

    # VIZUÁLIS DEBUG: Ezt a képet nézd meg a mappában futás után!
    cv2.imwrite("debug_mask.jpg", mask)

    contours, _ = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

    # Szűrés a kontúrok alakja és színpontossága alapján
    valid_contours = []
    for cnt in contours:
        area = cv2.contourArea(cnt)
        if area > min_area:
            # Ellenőrizzük a kontúr alakját (nem lehet túl szabálytalan)
            perimeter = cv2.arcLength(cnt, True)
            if perimeter > 0:
                circularity = 4 * np.pi * area / (perimeter * perimeter)
                # Túl szabálytalan alakzatok kiszűrése (pl. vonalszerű zaj)
                if circularity > 0.1:  # A kör alakú foltok circ=1, vonalak circ<0.1
                    # Színpontosság ellenőrzése
                    if verify_color_accuracy(image, cnt, target_hex, threshold=0.25):
                        valid_contours.append(cnt)
                    else:
                        print_debug(f"Kontúr területe: {area}, de színpontosság nem megfelelő")

    return mask, valid_contours


def extract_text_from_roi(image, roi_rect, expand=15):
    """
    Kivágja a megtalált foltot és egy kicsit körülötte lévő területet.
    """
    x, y, w, h = roi_rect
    x = max(0, x - expand)
    y = max(0, y - expand)
    w = min(image.shape[1] - x, w + 2 * expand)
    h = min(image.shape[0] - y, h + 2 * expand)

    roi = image[y:y + h, x:x + w]

    gray = cv2.cvtColor(roi, cv2.COLOR_BGR2GRAY)
    # Kontraszt növelése az OCR számára
    _, thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

    # A --psm 6 (Assume a single uniform block of text) a legjobb a matricákhoz
    text = pytesseract.image_to_string(thresh, lang='hun+eng', config='--psm 6').lower()
    return text


def contains_sticker_pattern(text):
    # Tanév/szemeszter kulcsszavak
    keywords = [
        'tanév', 'tanev', 'félév', 'felev', 'szemeszter',
        'évfolyam', 'evfolyam', 'szak', 'tanfolyam',
        'diák', 'diak', 'igazolvány', 'igazolvany'
    ]

    # 1. Évszám elválasztóval (pl: 2023/24, 23/24)
    if re.search(r'\d{2,4}\s*[/\-]\s*\d{2,4}', text, re.IGNORECASE):
        return True

    # 2. Bármilyen szám jelenléte (már ez is elég)
    if re.search(r'\d+', text):
        # Ha van szám, akkor elég, ha legalább 3 karakter hosszú a szöveg
        if len(text.strip()) >= 3:
            return True

    # 3. Kulcsszavak jelenléte (szám nélkül is)
    if any(keyword in text for keyword in keywords):
        return True

    # 4. Ha a szöveg legalább 5 karakter hosszú (szinte mindent elfogad)
    if len(text.strip()) >= 5:
        return True

    return False


def validate_student_card(image_path, target_hex_color="#B300B3"):
    result = {
        "is_student_card": False,
        "sticker_found": False,
        "is_valid": False,
        "debug_info": "",
        "error": None,
        "color_matches": 0,
        "text_matches": False
    }

    try:
        img = cv2.imread(image_path)
        if img is None:
            result["error"] = "Kép nem található"
            return result

        img_small = resize_to_limit(img, 800)
        print_debug(f"Keresett HEX szín: {target_hex_color}")

        target_hsv = get_target_hsv(target_hex_color)
        print_debug(f"Cél HSV értékek: H={target_hsv[0]}, S={target_hsv[1]}, V={target_hsv[2]}")

        # 1. SZÍN KERESÉSE
        mask, contours = find_color_blobs(img_small, target_hex_color, min_area=300)

        if contours:
            result["sticker_found"] = True
            result["color_matches"] = len(contours)
            result["debug_info"] += f"Találat a megadott színnel: {len(contours)} folt. "

            # 2. Szöveg keresése a megtalált foltokon
            sticker_valid_text = False
            for i, cnt in enumerate(contours):
                x, y, w, h = cv2.boundingRect(cnt)
                roi_text = extract_text_from_roi(img_small, (x, y, w, h))
                print_debug(f"Folt {i + 1} szovege: {roi_text[:100]}")

                # Debug képek mentése az ellenőrzéshez
                roi_img = img_small[y:y + h, x:x + w]
                cv2.imwrite(f"debug_roi_{i}.jpg", roi_img)

                if contains_sticker_pattern(roi_text):
                    sticker_valid_text = True
                    result["text_matches"] = True
                    result["debug_info"] += f"Matrica szoveg minta is megtalalva a folton. "
                    break

            # Mindkét feltétel teljesülése esetén érvényes a diákigazolvány
            if sticker_valid_text:
                result["is_student_card"] = True
                result["is_valid"] = True
                result["debug_info"] += "Szin es szoveg minta alapjan elfogadva. "
            else:
                result["debug_info"] += "A folton nem volt olvashato matrica szoveg, a szin onmagaban nem elegendo. "

        else:
            result["debug_info"] += "Nincs a megadott szinu folt a kepen. "

            # 3. BIZTOSÍTÉK: Ha a szín nem található, OCR az egész képen
            print_debug("Szín nem található. Teljes kép OCR indítása...")
            full_text = pytesseract.image_to_string(img_small, lang='hun+eng').lower()
            print_debug(f"Teljes szöveg: {full_text[:200]}")

            if contains_sticker_pattern(full_text) or "diákigazolvány" in full_text or "diakigazolvany" in full_text or 'diakigazolvan' in full_text or 'diak' in full_text:
                result["is_student_card"] = True
                result["text_matches"] = True
                result[
                    "debug_info"] += "Szoveg alapjan diakigazolvany, de a matrica szine nem lathato a fenyviszonyok miatt. "
            else:
                result["debug_info"] += "Szoveg minta sem talalhato a kepen. "

        print_debug(f"EREDMENY: {json.dumps(result)}")
        return result

    except Exception as e:
        result["error"] = str(e)
