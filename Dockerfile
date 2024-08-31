FROM ubuntu:22.04
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y tesseract-ocr && apt-get install -y python3 python3-pip
ENTRYPOINT ["tesseract"]

# docker run --rm -v ruta_donde_está_la_imagen/:/mnt tesseract:0.1 /mnt/Nombre_imagen.png stdout