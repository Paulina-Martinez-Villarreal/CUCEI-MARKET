import csv
import os
import subprocess

# Función para determinar la estación en función del mes
def obtener_estacion(mes):
    if mes in [12, 1, 2]:
        return "Invierno"
    elif mes in [3, 4, 5]:
        return "Primavera"
    elif mes in [6, 7, 8]:
        return "Verano"
    elif mes in [9, 10, 11]:
        return "Otoño"
    else:
        raise ValueError("Mes inválido.")

# Función para generar datos simulados
def generar_datos():
    datos = []
    crecimiento_anual = 1.02  # Crecimiento del 2% anual
    usuarios_base = 10  # Usuarios base en 1800

    for año in range(1800, 2025):  # Desde 1800 hasta 2024
        for mes in range(1, 13):  # Meses del 1 al 12
            # Calcular el número de usuarios nuevos con crecimiento anual
            usuarios = usuarios_base * (crecimiento_anual ** (año - 1800))
            
            # Añadir fluctuaciones estacionales
            if obtener_estacion(mes) == "Verano":
                usuarios *= 1.2  # 20% más en verano
            elif obtener_estacion(mes) == "Invierno":
                usuarios *= 0.8  # 20% menos en invierno
            
            # Redondear el número de usuarios
            usuarios = round(usuarios)
            
            # Añadir el registro a los datos
            datos.append([mes, usuarios, obtener_estacion(mes), año])
    
    return datos

# Guardar los datos en un archivo CSV
def guardar_csv(datos, nombre_archivo="datos.csv"):
    # Especificar la ruta del escritorio
    escritorio = os.path.join(os.path.expanduser("~"), "Desktop")
    ruta_completa = os.path.join(escritorio, nombre_archivo)
    
    with open(ruta_completa, mode="w", newline="", encoding="utf-8") as archivo:
        escritor = csv.writer(archivo)
        escritor.writerow(["Mes", "UsuariosNuevos", "Estacion", "Año"])  # Cabecera
        escritor.writerows(datos)  # Datos
    
    return ruta_completa

# Generar los datos y guardarlos en un archivo CSV
datos = generar_datos()
ruta_archivo = guardar_csv(datos)

# Imprimir la ruta del archivo
print(f"Archivo 'datos.csv' generado correctamente en: {ruta_archivo}")

# Abrir la carpeta donde se guardó el archivo
if os.name == "nt":  # Windows
    subprocess.run(["explorer", os.path.dirname(ruta_archivo)], shell=True)
elif os.name == "posix":  # macOS o Linux
    subprocess.run(["open", os.path.dirname(ruta_archivo)])
else:
    print("No se pudo abrir la carpeta automáticamente. Por favor, busca el archivo manualmente.")