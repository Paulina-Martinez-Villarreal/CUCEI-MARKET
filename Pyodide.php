<?php
session_start(); // Iniciar sesión

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html"); // Redirige a la página principal
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predicción de Usuarios</title>
    <script src="https://cdn.jsdelivr.net/pyodide/v0.23.4/full/pyodide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            color: #444;
            text-align: center;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        #resultado {
            font-size: 1.2em;
            color: #28a745;
            margin-top: 20px;
            text-align: center;
        }
        #debug {
            color: #888;
            font-size: 0.9em;
            margin-top: 20px;
            text-align: center;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #28a745;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            display: none;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .chart-container {
            margin-top: 40px;
            max-width: 800px;
            margin: 40px auto;
        }
        .export-buttons {
            text-align: center;
            margin-top: 20px;
        }
		
		        /* Flecha para regresar */
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #004080;
            font-size: 18px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .back-arrow i {
            margin-right: 8px;
        }
        .back-arrow:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
<!-- Flecha para regresar -->
<a href="verificar_usuarios.php" class="back-arrow">
    <i class="fas fa-arrow-left"> Atrás</i>
</a>
    <h1>Bienvenido a la IA de Predicción de Usuarios</h1>
	
    <div class="form-container">
        <label for="mes">Mes:</label>
        <input type="number" id="mes" min="1" max="12" placeholder="1-12">
        <label for="año">Año:</label>
        <input type="number" id="año" min="2023" placeholder="2023 o posterior">
        <button onclick="predecirUsuarios()" id="boton-predecir">Predecir</button>
        <div class="loader" id="loader"></div>
        <p id="resultado"></p>
        <p id="debug"></p>
    </div>

    <div class="chart-container">
        <canvas id="graficoUsuarios"></canvas>
    </div>

    <div class="chart-container">
        <canvas id="graficoComparacion"></canvas>
    </div>

    <div class="export-buttons">
        <button onclick="exportarCSV()">Exportar a CSV</button>
        <button onclick="exportarPDF()">Exportar a PDF</button>
    </div>

    <script>
        // Variable global para Pyodide
        let pyodide;

        // Variables globales para los gráficos
        let graficoUsuarios;
        let graficoComparacion;

        // Variable global para almacenar datos históricos y predicciones
        let datosHistoricos = [];
        let predicciones = [];

        // Función para determinar la estación en función del mes
        function obtenerEstacion(mes) {
            if (mes === 12 || mes === 1 || mes === 2) {
                return "Invierno";
            } else if (mes >= 3 && mes <= 5) {
                return "Primavera";
            } else if (mes >= 6 && mes <= 8) {
                return "Verano";
            } else if (mes >= 9 && mes <= 11) {
                return "Otoño";
            } else {
                throw new Error("Mes inválido.");
            }
        }

        async function main() {
            // Mostrar mensaje de carga
            document.getElementById("debug").innerText = "Cargando Pyodide...";

            // Cargar Pyodide
            pyodide = await loadPyodide();
            document.getElementById("debug").innerText = "Pyodide cargado correctamente.";

            // Cargar bibliotecas necesarias
            document.getElementById("debug").innerText += "\nCargando bibliotecas...";
            await pyodide.loadPackage(["pandas", "numpy", "scikit-learn", "joblib"]);
            document.getElementById("debug").innerText += "\nBibliotecas cargadas correctamente.";

            // Cargar el archivo datos.csv
            document.getElementById("debug").innerText += "\nCargando datos...";
            let response = await fetch("datos.csv");
            let csvData = await response.text();
            document.getElementById("debug").innerText += "\nArchivo datos.csv cargado correctamente.";

            // Definir el código Python
            let pythonCode = `
import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.preprocessing import OneHotEncoder, StandardScaler, PolynomialFeatures
from io import StringIO
import joblib
import os
import json

# Cargar datos desde el archivo CSV
csv_data = """${csvData}"""
df = pd.read_csv(StringIO(csv_data))
print("Datos cargados correctamente:")
print(df.head())

# Seleccionar y reordenar columnas necesarias
df = df[["Mes", "Estacion", "Año", "UsuariosNuevos"]]

# Preprocesamiento
X = df[["Mes", "Estacion", "Año"]]
Y = df["UsuariosNuevos"].values

# Codificar la columna "Estacion"
onehot_encoder = OneHotEncoder(sparse_output=False)
X_estacion = onehot_encoder.fit_transform(X[["Estacion"]])

# Normalizar las características numéricas
scaler = StandardScaler()
X_normalizado = scaler.fit_transform(X[["Mes", "Año"]])

# Concatenar características normalizadas y codificadas
X = np.hstack([X_normalizado, X_estacion])

# Añadir características polinómicas
poly = PolynomialFeatures(degree=2, include_bias=False)
X_poly = poly.fit_transform(X)

# Entrenar el modelo (solo si no existe un modelo guardado)
if not os.path.exists("modelo_usuarios.pkl"):
    print("Entrenando modelo...")
    model = RandomForestRegressor(n_estimators=100, random_state=42)
    model.fit(X_poly, Y)
    joblib.dump(model, "modelo_usuarios.pkl")
    print("Modelo entrenado y guardado correctamente.")
else:
    print("Cargando modelo guardado...")
    model = joblib.load("modelo_usuarios.pkl")
    print("Modelo cargado correctamente.")

# Función para predecir usuarios
def predecir(mes, año, estacion):
    # Normalizar el mes y el año
    entrada_normalizada = scaler.transform([[mes, año]])
    # Codificar la estación
    estacion_codificada = onehot_encoder.transform([[estacion]])
    # Concatenar características
    entrada_completa = np.hstack([entrada_normalizada, estacion_codificada])
    # Añadir características polinómicas
    entrada_polinomica = poly.transform(entrada_completa)
    # Predecir
    return model.predict(entrada_polinomica)[0]

# Obtener datos históricos para el gráfico
def obtener_datos_historicos():
    datos = df.to_dict(orient="records")
    return json.dumps(datos)
            `;

            // Ejecutar el código Python
            document.getElementById("debug").innerText += "\nEjecutando código Python...";
            pyodide.runPython(pythonCode);
            document.getElementById("debug").innerText += "\nCódigo Python ejecutado correctamente.";

            // Obtener datos históricos para el gráfico
            datosHistoricos = JSON.parse(pyodide.globals.get("obtener_datos_historicos")());

            // Inicializar la lista de predicciones con valores vacíos
            predicciones = new Array(datosHistoricos.length).fill("");

            // Crear gráficos
            crearGraficoUsuarios(datosHistoricos);
            crearGraficoComparacion(datosHistoricos);

            // Habilitar el botón de predicción
            document.getElementById("boton-predecir").disabled = false;
            document.getElementById("debug").innerText += "\nListo para hacer predicciones.";
        }

        // Función para crear el gráfico de usuarios
        function crearGraficoUsuarios(datos) {
            let ctx = document.getElementById("graficoUsuarios").getContext("2d");
            let labels = datos.map(d => `${d["Año"]}-${d["Mes"]}`);
            let valores = datos.map(d => d["UsuariosNuevos"]);

            graficoUsuarios = new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Usuarios Nuevos",
                        data: valores,
                        borderColor: "#28a745",
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: "Fecha (Año-Mes)"
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: "Usuarios Nuevos"
                            }
                        }
                    }
                }
            });
        }

        // Función para crear el gráfico de comparación
        function crearGraficoComparacion(datos) {
            let ctx = document.getElementById("graficoComparacion").getContext("2d");
            let labels = datos.map(d => `${d["Año"]}-${d["Mes"]}`);
            let valoresReales = datos.map(d => d["UsuariosNuevos"]);

            graficoComparacion = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Datos Reales",
                        data: valoresReales,
                        backgroundColor: "#28a745",
                    }, {
                        label: "Predicciones",
                        data: predicciones,
                        backgroundColor: "#007bff",
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: "Fecha (Año-Mes)"
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: "Usuarios Nuevos"
                            }
                        }
                    }
                }
            });
        }

        // Función para predecir usuarios desde JavaScript
        window.predecirUsuarios = async function() {
            try {
                // Obtener valores del formulario
                let mes = parseInt(document.getElementById("mes").value);
                let año = parseInt(document.getElementById("año").value);

                // Validar entradas
                if (mes < 1 || mes > 12) {
                    throw new Error("El mes debe estar entre 1 y 12.");
                }
                if (año < 2023) {
                    throw new Error("El año debe ser 2023 o posterior.");
                }

                // Determinar la estación automáticamente
                let estacion = obtenerEstacion(mes);

                // Mostrar spinner y deshabilitar botón
                document.getElementById("loader").style.display = "block";
                document.getElementById("boton-predecir").disabled = true;
                document.getElementById("resultado").innerText = "";

                // Llamar a la función de predicción en Python
                let prediccion = pyodide.globals.get("predecir")(mes, año, estacion);

                // Mostrar el resultado
                document.getElementById("resultado").innerText = `Predicción: ${prediccion.toFixed(2)} usuarios nuevos`;

                // Crear un nuevo registro para los datos históricos
                let nuevoRegistro = {
                    Mes: mes,
                    Año: año,
                    Estacion: estacion,
                    UsuariosNuevos: null, // No hay datos reales, es una predicción
                    Prediccion: prediccion
                };

                // Agregar el nuevo registro a los datos históricos
                datosHistoricos.push(nuevoRegistro);

                // Agregar la predicción a la lista de predicciones
                predicciones.push(prediccion);

                // Actualizar los gráficos
                graficoUsuarios.data.labels.push(`${año}-${mes}`);
                graficoUsuarios.data.datasets[0].data.push(prediccion);
                graficoUsuarios.update();

                graficoComparacion.data.labels.push(`${año}-${mes}`);
                graficoComparacion.data.datasets[1].data.push(prediccion);
                graficoComparacion.update();
            } catch (error) {
                document.getElementById("resultado").innerText = `Error: ${error.message}`;
            } finally {
                // Ocultar spinner y habilitar botón
                document.getElementById("loader").style.display = "none";
                document.getElementById("boton-predecir").disabled = false;
            }
        };

        // Función para exportar datos a CSV
        function exportarCSV() {
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Año,Mes,Estacion,UsuariosNuevos,Prediccion\n";

            datosHistoricos.forEach((d, i) => {
                let prediccion = predicciones[i] || ""; // Usar predicción si existe, de lo contrario vacío
                csvContent += `${d["Año"]},${d["Mes"]},${d["Estacion"]},${d["UsuariosNuevos"] || ""},${prediccion}\n`;
            });

            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "datos_usuarios.csv");
            document.body.appendChild(link);
            link.click();
        }

        // Función para exportar datos a PDF
        async function exportarPDF() {
            const { jsPDF } = window.jspdf;
            let doc = new jsPDF();

            // Título
            doc.setFontSize(18);
            doc.text("Reporte de Usuarios Nuevos", 10, 10);

            // Convertir gráficos a imágenes
            let canvas1 = document.getElementById("graficoUsuarios");
            let canvas2 = document.getElementById("graficoComparacion");

            let img1 = await html2canvas(canvas1);
            let img2 = await html2canvas(canvas2);

            // Agregar imágenes al PDF
            doc.addImage(img1, "PNG", 10, 20, 180, 80);
            doc.addImage(img2, "PNG", 10, 110, 180, 80);

            // Guardar el PDF
            doc.save("datos_usuarios.pdf");
        }

        // Llamar a la función principal
        main().catch(error => {
            document.getElementById("debug").innerText += `\nError en main(): ${error.message}`;
        });
    </script>
</body>
</html>