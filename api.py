import os
import subprocess
from flask import Flask, request, jsonify, render_template, send_file
import nmap
import socket
from reportlab.lib.pagesizes import letter
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer
from reportlab.lib.styles import getSampleStyleSheet
import io
import json

app = Flask(__name__)

SERVICE_DESCRIPTIONS = {
    'http': "HTTP est utilisé pour la communication web. Assurez-vous d'utiliser HTTPS pour sécuriser les données en transit.",
    'https': "HTTPS sécurise la communication web en cryptant les données en transit. Assurez-vous d'avoir un certificat valide.",
    'ssh': "SSH est utilisé pour les connexions sécurisées à distance. Utilisez des clés SSH plutôt que des mots de passe pour une meilleure sécurité.",
    'ftp': "FTP est utilisé pour le transfert de fichiers. Utilisez FTPS ou SFTP pour sécuriser les transferts.",
}

def est_adresse_ip(adresse):
    try:
        socket.inet_aton(adresse)
        return True
    except socket.error:
        return False

def resoudre_nom_domaine(domaine):
    try:
        return socket.gethostbyname(domaine)
    except socket.gaierror:
        return None

def scan_nmap(ip):
    scanner = nmap.PortScanner()
    try:
        scanner.scan(ip, arguments='-p-')
        return dict(scanner[ip]['tcp'])
    except nmap.PortScannerError as e:
        return {"error": str(e)}

def scan_gobuster(target):
    # Uses common Kali Linux wordlist, adjust path if needed
    wordlist = "/usr/share/wordlists/dirb/common.txt"
    cmd = f"gobuster dir -u http://{target} -w {wordlist} -q"
    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    return result.stdout

def run_nikto(target):
    cmd = f"nikto -h {target}"
    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    return result.stdout

def run_nuclei(target):
    cmd = f"nuclei -u {target}"
    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    return result.stdout

def generer_rapport(ip, choix, nmap_result=None, gobuster_result=None, nikto_result=None, nuclei_result=None):
    rapport = f"Rapport d'analyse de sécurité pour la cible : {ip}\n\n"

    if 'ports_services' in choix and nmap_result:
        rapport += "Scan Nmap (Découverte de Ports et Services) :\n"
        if isinstance(nmap_result, dict):
            for port, service in nmap_result.items():
                service_name = service['name']
                description = SERVICE_DESCRIPTIONS.get(service_name, 'Aucune description disponible.')
                rapport += f"- Port {port} : Service {service_name} - {service['product']} {service['version']}\n"
                rapport += f"  Description : {description}\n"
        else:
            rapport += nmap_result
        rapport += "\n"

    if 'gobuster' in choix and gobuster_result:
        rapport += "Scan Gobuster (Répertoires) :\n"
        rapport += gobuster_result if gobuster_result else "Aucun répertoire trouvé.\n"
        rapport += "\n"

    if 'nikto' in choix and nikto_result:
        rapport += "Résultats du scan Nikto :\n"
        rapport += nikto_result
        rapport += "\n"

    if 'nuclei' in choix and nuclei_result:
        rapport += "Résultats du scan Nuclei :\n"
        rapport += nuclei_result
        rapport += "\n"

    return rapport

def generer_pdf(rapport):
    buffer = io.BytesIO()
    doc = SimpleDocTemplate(buffer, pagesize=letter)
    styles = getSampleStyleSheet()
    elements = []

    style_title = styles['Title']
    style_normal = styles['BodyText']

    # Title
    title = Paragraph("Rapport d'analyse de sécurité", style_title)
    elements.append(title)
    elements.append(Spacer(1, 12))

    # Content
    for line in rapport.split('\n'):
        paragraph = Paragraph(line, style_normal)
        elements.append(paragraph)
        elements.append(Spacer(1, 12))

    doc.build(elements)
    buffer.seek(0)
    return buffer

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/scan', methods=['POST'])
def scan():
    cible = request.form['cible']
    choix = request.form.getlist('choix')

    if not est_adresse_ip(cible):
        ip_cible = resoudre_nom_domaine(cible)
        if ip_cible is None:
            return jsonify({"error": "Le nom de domaine fourni est invalide ou introuvable."}), 400
    else:
        ip_cible = cible

    nmap_result = None
    gobuster_result = None
    nikto_result = None
    nuclei_result = None

    if 'ports_services' in choix:
        nmap_result = scan_nmap(ip_cible)

    if 'gobuster' in choix:
        gobuster_result = scan_gobuster(ip_cible)

    if 'nikto' in choix:
        nikto_result = run_nikto(ip_cible)

    if 'nuclei' in choix:
        nuclei_result = run_nuclei(ip_cible)

    rapport = generer_rapport(
        ip_cible,
        choix,
        nmap_result,
        gobuster_result,
        nikto_result,
        nuclei_result
    )

    return render_template('resultat.html', rapport=rapport)

@app.route('/download_pdf', methods=['POST'])
def download_pdf():
    rapport = request.form['rapport']
    pdf = generer_pdf(rapport)
    return send_file(pdf, as_attachment=True, download_name='rapport_scan.pdf', mimetype='application/pdf')

if __name__ == "__main__":
    app.run(debug=True)
