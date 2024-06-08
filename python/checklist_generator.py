import argparse
import mysql.connector
import pandas as pd
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.platypus import SimpleDocTemplate, Table, TableStyle
import html
import json

def sanitize(input_string):
    return html.escape(input_string.strip(), quote=True)

def erase_program_names(program_names, erased_program_names=None, profile=''):
    if erased_program_names is not None:
        if profile not in ['admin', 'superadmin']:
            return sanitize(', '.join(set(program_names.split(', ')) - set(erased_program_names)))
        else:
            program_array = {program_name: program_name[:-2] for program_name in program_names.split(', ')}
            return sanitize(', '.join(set(program_array.keys()) - set(erased_program_names)))
    else:
        return sanitize(program_names)

def good_practices_select(where_is=None, order_by=None, erased_goodpractices=None, erased_programs=None, profile=''):
    conn = mysql.connector.connect(
        host='localhost',
        user='checklist',
        password='9MPwevIT(zFTEgMp',
        database='checklist'
    )
    cursor = conn.cursor(dictionary=True)
    
    if profile not in ['admin', 'superadmin']:
        sql = """
            SELECT 
                GOODPRACTICE.goodpractice_id,
                GROUP_CONCAT(DISTINCT PROGRAM.program_name ORDER BY PROGRAM.program_name SEPARATOR ', ') AS program_names,
                PHASE.phase_name,
                GOODPRACTICE.item,
                GROUP_CONCAT(DISTINCT KEYWORD.onekeyword ORDER BY KEYWORD.onekeyword SEPARATOR ', ') AS keywords
            FROM GOODPRACTICE
            INNER JOIN PHASE ON GOODPRACTICE.phase_id = PHASE.phase_id
            INNER JOIN GOODPRACTICE_PROGRAM ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_PROGRAM.goodpractice_id
            INNER JOIN PROGRAM ON GOODPRACTICE_PROGRAM.program_id = PROGRAM.program_id
            INNER JOIN GOODPRACTICE_KEYWORD ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_KEYWORD.goodpractice_id
            INNER JOIN KEYWORD ON GOODPRACTICE_KEYWORD.keyword_id = KEYWORD.keyword_id
            WHERE GOODPRACTICE.is_hidden = FALSE AND GOODPRACTICE_PROGRAM.is_hidden = FALSE
        """
    else:
        sql = """
            SELECT
                GOODPRACTICE.goodpractice_id,
                GOODPRACTICE.is_hidden AS goodpractice_is_hidden,
                GROUP_CONCAT(DISTINCT CONCAT(PROGRAM.program_name, ':', GOODPRACTICE_PROGRAM.is_hidden) ORDER BY PROGRAM.program_name SEPARATOR ', ') AS program_names,
                PHASE.phase_name,
                GOODPRACTICE.item,
                GROUP_CONCAT(DISTINCT KEYWORD.onekeyword ORDER BY KEYWORD.onekeyword SEPARATOR ', ') AS keywords
            FROM GOODPRACTICE
            INNER JOIN PHASE ON GOODPRACTICE.phase_id = PHASE.phase_id
            INNER JOIN GOODPRACTICE_PROGRAM ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_PROGRAM.goodpractice_id
            INNER JOIN PROGRAM ON GOODPRACTICE_PROGRAM.program_id = PROGRAM.program_id
            INNER JOIN GOODPRACTICE_KEYWORD ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_KEYWORD.goodpractice_id
            INNER JOIN KEYWORD ON GOODPRACTICE_KEYWORD.keyword_id = KEYWORD.keyword_id
        """
    
    params = []

    # Adding WHERE clauses
    if where_is:
        where_clause_start = " AND (" if profile not in ['admin', 'superadmin'] else " WHERE ("
        where_clause = ''
        for column, filters in where_is.items():
            for i, value in enumerate(filters):
                if value:
                    where_clause += f"{column} = %s OR "
                    params.append(value)
            where_clause = where_clause.rstrip("OR ") + ") AND ("
        where_clause = where_clause.rstrip("AND (")
        sql += where_clause_start + where_clause
    
    # Exclude erased good practices
    if erased_goodpractices:
        erased_ids = ', '.join('%s' for _ in erased_goodpractices)
        sql += f" AND GOODPRACTICE.goodpractice_id NOT IN ({erased_ids})"
        params.extend(erased_goodpractices)

    sql += ' GROUP BY GOODPRACTICE.item'

    # Adding ORDER BY clause
    if order_by:
        column, ascending = list(order_by.items())[0]
        direction = 'ASC' if ascending else 'DESC'
        sql += f" ORDER BY {column} {direction}"
    
    print(sql)
    cursor.execute(sql, params)
    good_practices = cursor.fetchall()

    conn.close()
    
    # Handle erased programs
    if erased_programs:
        for good_practice in good_practices:
            gp_id = good_practice['goodpractice_id']
            if f'id{gp_id}' in erased_programs:
                good_practice['program_names'] = erase_program_names(good_practice['program_names'], erased_programs[f'id{gp_id}'], profile)
                if not good_practice['program_names']:
                    good_practices.remove(good_practice)

    return good_practices

def export_to_csv(data, filename):
    df = pd.DataFrame(data)
    df.to_csv(filename, index=False)

def export_to_pdf(data, filename):
    df = pd.DataFrame(data)
    
    pdf = SimpleDocTemplate(filename, pagesize=letter)
    elements = []
    
    # Create a table with the data
    table_data = [df.columns.tolist()] + df.values.tolist()
    table = Table(table_data)
    
    # Add style to the table
    style = TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('FONTSIZE', (0, 0), (-1, 0), 12),
        ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
        ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
        ('GRID', (0, 0), (-1, -1), 1, colors.black),
    ])
    table.setStyle(style)
    
    elements.append(table)
    pdf.build(elements)

def main():
    parser = argparse.ArgumentParser(description='Export good practices to CSV or PDF.')
    parser.add_argument('--where', type=str, help='JSON string for WHERE clause filters.')
    parser.add_argument('--order', type=str, help='JSON string for ORDER BY clause.')
    parser.add_argument('--erase_goodpractices', type=str, help='Comma-separated list of goodpractice IDs to erase.')
    parser.add_argument('--erase_programs', type=str, help='JSON string for programs to erase.')
    parser.add_argument('--profile', type=str, required=True, help='Profile type.')
    parser.add_argument('--output_format', type=str, choices=['csv', 'pdf'], required=True, help='Output format (csv or pdf).')
    parser.add_argument('--output_file', type=str, required=True, help='Output file name.')

    args = parser.parse_args()

    where_is = json.loads(args.where) if args.where else None
    order_by = json.loads(args.order) if args.order else None
    erased_goodpractices = args.erase_goodpractices.split(',') if args.erase_goodpractices else None
    erased_programs = json.loads(args.erase_programs) if args.erase_programs else None
    profile = args.profile
    output_format = args.output_format
    output_file = args.output_file

    data = good_practices_select(
        where_is=where_is,
        order_by=order_by,
        erased_goodpractices=erased_goodpractices,
        erased_programs=erased_programs,
        profile=profile
    )

    if output_format == 'csv':
        export_to_csv(data, output_file)
    elif output_format == 'pdf':
        export_to_pdf(data, output_file)

if __name__ == "__main__":
    main()
