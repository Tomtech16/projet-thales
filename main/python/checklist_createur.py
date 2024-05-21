# Programme python qui va recupere les infos du php puis se connecter a la bdd pour ensuite cree un pdf avec les bonnes pratiqus.
# pip install reportlab

#Programme :
import os
import time
import json
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas


json_dir = '../json'
json_filename = 'bonne_pratique.json'
json_path = os.path.join(json_dir, json_filename)

def wait_for_json_file(filepath, timeout=300, check_interval=5):
    """Wait for a JSON file to appear in the specified directory."""
    start_time = time.time()
    while (time.time() - start_time) < timeout:
        if os.path.exists(filepath):
            return True
        time.sleep(check_interval)
    return False

def read_json(filepath):
    """Read and return the data from the JSON file."""
    with open(filepath, 'r', encoding='utf-8') as f:
        data = json.load(f)
    return data

def create_pdf(data, pdf_path='bonne_pratique.pdf'):
    """Create a PDF document from the JSON data."""
    c = canvas.Canvas(pdf_path, pagesize=letter)
    width, height = letter
    y = height - 50

   
    c.setFont("Helvetica-Bold", 16)
    c.drawString(100, y, "Bonne Pratique")
    y -= 40


    c.setFont("Helvetica", 12)
    for item in data:
        if y < 100: 
            c.showPage()
            c.setFont("Helvetica", 12)
            y = height - 50

        c.drawString(100, y, f"ID: {item['goodpractice_id']}")
        y -= 20
        c.drawString(100, y, f"Program Names: {item['program_names']}")
        y -= 20
        c.drawString(100, y, f"Phase: {item['phase_name']}")
        y -= 20
        c.drawString(100, y, f"Item: {item['item']}")
        y -= 20
        c.drawString(100, y, f"Keywords: {item['keywords']}")
        y -= 40  

    c.save()

def main():
    print(f"Waiting for JSON file: {json_filename}")
    if wait_for_json_file(json_path):
        print(f"Found JSON file: {json_filename}")
        
        data = read_json(json_path)
        
        create_pdf(data)
        
        print("PDF created successfully.")
    else:
        print("Timeout waiting for JSON file.")

if __name__ == '__main__':
    main()
