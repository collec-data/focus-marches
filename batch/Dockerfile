# set base image (host OS)
FROM python:3.9.5-slim-buster

RUN apt-get update

# set the working directory in the container
WORKDIR /appli
RUN mkdir /appli/IN
COPY requirements.txt .
RUN pip install -r requirements.txt

COPY scripts scripts

EXPOSE 80
CMD ["python", "python scripts/import_decp.py"]

