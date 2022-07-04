FROM postgres:11.5-alpine

# Variables needed at runtime to configure postgres and run the initdb scripts
ENV POSTGRES_DB 'alumniNWS'
ENV POSTGRES_USER 'postgres'
ENV POSTGRES_PASSWORD '5487'

# Copy in the load-extensions script
COPY load-extensions.sh /docker-entrypoint-initdb.d/
#COPY extension.sql /docker-entrypoint-initdb.d/