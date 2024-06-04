import json
from reportlab.pdfgen import canvas

# Step 1: Retrieve the JSON file
# Replace 'url' with the actual URL to fetch the JSON file
response = requests.get(url)
json_data = response.json()

# Step 2: Parse the JSON file
markers = json_data['markers']
excluded_ids = json_data['excluded_ids']

# Step 3: Generate PDF content
pdf_content = ""
for marker in markers:
    if marker['id'] not in excluded_ids:
        pdf_content += f"{marker['title']}: {marker['description']}\n"

# Step 4: Create the PDF file
pdf_file = "/path/to/output.pdf"
c = canvas.Canvas(pdf_file)
c.setFont("Helvetica", 12)
c.drawString(100, 700, pdf_content)
c.save()

# Step 5: Save the PDF file
print(f"PDF file saved at: {pdf_file}")