import mysql.connector

def get_db_connection():
    conn = mysql.connector.connect(
        host='',
        user='',
        password='',
        database=''
    )
    return conn
