from flask import Flask, request, jsonify, render_template
import subprocess
import os

app = Flask(__name__)

# Configurar la ruta para almacenar temporalmente los archivos subidos
UPLOAD_FOLDER = 'uploads'
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

# Asegúrate de que la carpeta de subidas exista
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/run-docker', methods=['POST'])
def run_docker():
    # Obtener datos del formulario
    if 'file' not in request.files or 'dockerTag' not in request.form:
        return jsonify({"error": "Faltan datos en la solicitud"}), 400

    file = request.files['file']
    docker_tag = request.form['dockerTag']

    if file.filename == '':
        return jsonify({"error": "No se seleccionó ningún archivo"}), 400

    # Reemplazar espacios en el nombre del archivo por guiones bajos
    filename = file.filename.replace(' ', '_')
    
    # Guardar el archivo subido en la carpeta de subidas
    file_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
    file.save(file_path)

    # Convertir la ruta a un formato que Docker entienda
    abs_file_path = os.path.abspath(file_path)
    abs_dir_path = os.path.dirname(abs_file_path)
    command = f"docker run --rm -v {abs_dir_path}:/mnt {docker_tag} /mnt/{filename} stdout"

    try:
        # Ejecutar el comando
        result = subprocess.run(command, shell=True, check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        output = result.stdout.decode('utf-8')

        # Buscar la palabra "pistola" en el resultado
        found_word = "TRUE" if "pistola" in output.lower() else "FALSE"
        
        return jsonify({"result": output, "found": found_word})
    except subprocess.CalledProcessError as e:
        return jsonify({"error": e.stderr.decode('utf-8')}), 500

if __name__ == '__main__':
    app.run(debug=True)
