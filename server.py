from flask import Flask, request, jsonify
import requests
import cv2
import numpy as np
import os

from validator import validate_student_card

app = Flask(__name__)

def download_image(url, save_path):
    try:
        response = requests.get(url, stream=True, timeout=10)
        if response.status_code == 200:
            with open(save_path, 'wb') as f:
                f.write(response.content)
            return True
    except Exception as e:
        print(f"Hiba a letöltésnél: {e}")
    return False

@app.route('/check-card', methods=['POST'])
def check_card():
    data = request.json
    image_url = data.get('url')
    # Bekérjük a színt, alapértelmezetten lila, ha nincs megadva
    target_color = data.get('color', '#B300B3')

    if not image_url:
        return jsonify({"error": "Nincs URL megadva"}), 400

    print(f"--> Kérés érkezett. Kép: {image_url} | Keresett szín: {target_color}")

    temp_filename = "temp_scan.jpg"

    if download_image(image_url, temp_filename):
        try:
            # Átadjuk a színt a validátornak
            result = validate_student_card(temp_filename, target_color)
        except Exception as e:
            result = {"error": str(e)}

        if os.path.exists(temp_filename):
            os.remove(temp_filename)

        return jsonify(result)
    else:
        return jsonify({"error": "A képet nem sikerült letölteni a szerverről."}), 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
