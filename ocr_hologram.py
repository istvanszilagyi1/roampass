import sys
import cv2
import json
import numpy as np
import os

front_path = sys.argv[1]
back_path = sys.argv[2]

# referencia képek
ref_folder = 'storage/app/hologram_references/'
ref_images = [cv2.imread(os.path.join(ref_folder, f), 0) for f in os.listdir(ref_folder)]

# betöltés szürkeárnyalatban
front_img = cv2.imread(front_path, 0)
back_img = cv2.imread(back_path, 0)

def match_hologram(img):
    orb = cv2.ORB_create()
    kp1, des1 = orb.detectAndCompute(img, None)

    best_score = 0
    for ref in ref_images:
        kp2, des2 = orb.detectAndCompute(ref, None)
        if des2 is None or des1 is None:
            continue
        bf = cv2.BFMatcher(cv2.NORM_HAMMING, crossCheck=True)
        matches = bf.match(des1, des2)
        if matches:
            score = len(matches) / len(kp2) * 100
            if score > best_score:
                best_score = score
    return best_score

front_score = match_hologram(front_img)
back_score = match_hologram(back_img)

avg_score = (front_score + back_score) / 2

if avg_score > 80:
    status = 'high'
elif avg_score > 60:
    status = 'medium'
else:
    status = 'fail'

print(json.dumps({'status': status, 'confidence': round(avg_score, 2)}))
