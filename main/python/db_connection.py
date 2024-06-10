import mysql.connector

def get_db_connection():
    conn = mysql.connector.connect(
        host='localhost',
        user='checklist',
        password='9MPwevIT(zFTEgMp',
        database='checklist'
    )
    return conn