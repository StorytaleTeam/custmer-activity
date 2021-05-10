FROM ewwgenn/default_fpm_7.4:1.01
RUN apt update \
    && apt install -y \
        cron \
        systemd \
    && systemctl enable cron
COPY cron/crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab \
    && crontab /etc/cron.d/crontab
CMD ["cron", "-f"]