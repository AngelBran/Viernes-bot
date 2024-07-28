#!/bin/bash

# Define el archivo de configuración y una copia temporal
CONFIG_FILE="app.yaml"
TEMP_FILE="temp.yaml"

# Copia el contenido original a un archivo temporal
cp $CONFIG_FILE $TEMP_FILE

# Función para añadir variables de entorno al archivo YAML
add_env_var() {
    local name=$1
    local value=$2
    echo "  $name: $value" >> $TEMP_FILE
}

# Agrega las variables de entorno
add_env_var APP_URL "$APP_URL"
add_env_var BOT_API_KEY "$BOT_API_KEY"

# Reemplaza el archivo de configuración original con la versión modificada
mv $TEMP_FILE $CONFIG_FILE