FROM debian:bookworm

ENV PHP_VERSION=8.4

# Install base tools required to add third-party repositories.
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    gnupg \
    cron

RUN \
    # Prepare keyrings directory for third-party apt signing keys.
    install -m 0755 -d /etc/apt/keyrings \
    && curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /etc/apt/keyrings/sury-php.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/sury-php.gpg] https://packages.sury.org/php/ bookworm main" > /etc/apt/sources.list.d/sury-php.list \
    # Install PHP and extensions.
    && apt-get update && apt-get install -y --no-install-recommends \
        php${PHP_VERSION} \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-common \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-intl

# Reduce image size by removing transient apt metadata/caches.
RUN apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

# Copy local files into the container
COPY . /opt/schema-transformer

# Set up cron jobs
COPY ./.docker/run-cron-job.sh /opt/schema-transformer/.docker/run-cron-job.sh
COPY ./.docker/cronjobs /etc/cron.d/schema-cron
RUN chmod 0755 /opt/schema-transformer/.docker/run-cron-job.sh \
    && chmod 0644 /etc/cron.d/schema-cron

CMD ["cron", "-f"]