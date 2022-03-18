# set base image (host OS)
FROM python:3.9.5-slim-buster

RUN apt-get update

# set the working directory in the container
WORKDIR /appli

COPY requirements.txt .
RUN pip install -r requirements.txt

COPY IN IN
COPY scripts scripts

# set the working directory in the container
WORKDIR /appli/scripts
EXPOSE 80
CMD ["python", "import_decp.py"]

