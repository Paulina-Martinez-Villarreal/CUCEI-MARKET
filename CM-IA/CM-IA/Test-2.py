import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from tkinter import *
from tkinter import messagebox
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import OneHotEncoder, StandardScaler, PolynomialFeatures
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_squared_error

# Cargar datos
data = pd.read_csv("datos.csv")

# Preprocesamiento
X = data[["Mes", "Estacion", "Año"]]  # Incluir el año como característica
Y = data["UsuariosNuevos"].values

# Codificar la columna "Estacion"
onehot_encoder = OneHotEncoder(sparse_output=False)
X_estacion = onehot_encoder.fit_transform(X[["Estacion"]])

# Normalizar las características numéricas
scaler = StandardScaler()
X_normalizado = scaler.fit_transform(X[["Mes", "Año"]])

# Concatenar características normalizadas y codificadas
X = np.hstack([X_normalizado, X_estacion])  # Forma: (n_samples, 2 + n_encoded_features)

# Añadir características polinómicas
poly = PolynomialFeatures(degree=2, include_bias=False)
X_poly = poly.fit_transform(X)  # Forma: (n_samples, n_poly_features)

# Dividir datos en entrenamiento y validación
X_train, X_val, Y_train, Y_val = train_test_split(X_poly, Y, test_size=0.2, random_state=42)

# Usar Random Forest para mejorar el rendimiento
model = RandomForestRegressor(n_estimators=100, random_state=42)
model.fit(X_train, Y_train)

# Validación
h_val = model.predict(X_val)
mse_val = mean_squared_error(Y_val, h_val)
print(f"Error cuadrático medio (MSE) en validación: {mse_val:.4f}")

# Función para predecir usuarios
def predecir_usuarios():
    try:
        # Obtener el año y mes
        año_seleccionado = int(entry_año.get())
        mes_seleccionado = int(entry_mes.get())

        if mes_seleccionado not in meses:
            raise ValueError("Mes no válido. Por favor, selecciona un mes dentro del rango disponible.")

        if año_seleccionado < 2023:
            raise ValueError("Año no válido. Por favor, selecciona un año posterior o igual a 2023.")

        # Normalizar el mes y el año seleccionado
        entrada_normalizada = scaler.transform([[mes_seleccionado, año_seleccionado]])  # Forma: (1, 2)

        # Codificar la estación seleccionada
        estacion_codificada = onehot_encoder.transform([[estacion_seleccionada.get()]])  # Forma: (1, n_encoded_features)

        # Concatenar características normalizadas y codificadas
        entrada_completa = np.hstack([entrada_normalizada, estacion_codificada])  # Forma: (1, 2 + n_encoded_features)

        # Añadir características polinómicas
        entrada_polinomica = poly.transform(entrada_completa)  # Forma: (1, n_poly_features)

        # Predicción
        prediccion = model.predict(entrada_polinomica)[0]

        # Mostrar la predicción
        label_resultado.config(text=f"Predicción para {mes_seleccionado}/{año_seleccionado}: {prediccion:.2f} usuarios nuevos")

        # Guardar predicción en CSV
        predicciones_df = pd.DataFrame({"Año": [año_seleccionado], "Mes": [mes_seleccionado], "Prediccion_Usuarios": [prediccion]})
        nombre_archivo_predicciones = f"predicciones_usuarios_{año_seleccionado}_{mes_seleccionado}.csv"
        predicciones_df.to_csv(nombre_archivo_predicciones, index=False)

        # Mostrar mensaje de éxito
        messagebox.showinfo("Éxito", f"Predicciones guardadas en {nombre_archivo_predicciones}")

    except ValueError as e:
        messagebox.showerror("Error", str(e))

# Meses disponibles
meses = list(data["Mes"].unique())

# Interfaz gráfica
root = Tk()
root.title("Predicción de Usuarios Nuevos")

label_titulo = Label(root, text="Predicción de Usuarios Nuevos por Año y Mes", font=("Arial", 16))
label_titulo.pack(pady=10)

label_año = Label(root, text="Selecciona el año (2023 o posterior):", font=("Arial", 12))
label_año.pack(pady=5)
entry_año = Entry(root, width=10, font=("Arial", 12))
entry_año.pack(pady=5)

label_mes = Label(root, text="Selecciona el mes (1-12):", font=("Arial", 12))
label_mes.pack(pady=5)
entry_mes = Entry(root, width=10, font=("Arial", 12))
entry_mes.pack(pady=5)

label_estacion = Label(root, text="Selecciona la estación (Invierno, Primavera, Verano, Otoño):", font=("Arial", 12))
label_estacion.pack(pady=5)
estacion_seleccionada = Entry(root, width=15, font=("Arial", 12))
estacion_seleccionada.pack(pady=5)

boton_predecir = Button(root, text="Predecir Usuarios", command=predecir_usuarios, font=("Arial", 12))
boton_predecir.pack(pady=10)

label_resultado = Label(root, text="", font=("Arial", 12), fg="blue")
label_resultado.pack(pady=10)

root.mainloop()