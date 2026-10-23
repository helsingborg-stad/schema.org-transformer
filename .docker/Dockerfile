FROM php:8.4.27-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    cron \
    libicu-dev \
    && docker-php-ext-install intl \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

# Copy local files into the container
COPY . /opt/schema-transformer

# Set up cron jobs
COPY ./.docker/run-cron-job.sh /opt/schema-transformer/.docker/run-cron-job.sh
COPY ./.docker/cronjobs /etc/cron.d/schema-cron
COPY ./.docker/healthcheck /opt/schema-transformer/healthcheck
RUN chmod 0755 /opt/schema-transformer/.docker/run-cron-job.sh \
    && chmod 0644 /etc/cron.d/schema-cron

CMD ["sh", "-lc", "printenv > /etc/environment && php /opt/schema-transformer/healthcheck && exec cron -f"]