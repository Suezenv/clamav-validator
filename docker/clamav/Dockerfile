FROM debian:buster-slim

RUN apt update && apt install -y clamav vim clamdscan clamav-daemon clamav-testfiles 
RUN freshclam
RUN echo "TCPSocket 3310" >> /etc/clamav/clamd.conf \
    && sed -i 's/^Foreground .*$/Foreground true/g' /etc/clamav/clamd.conf \
    && sed -i 's/User clamav/User root/g' /etc/clamav/clamd.conf
RUN rm -rf /var/lib/apt/lists/* 
    #&& echo "TCPAddr 0.0.0.0" >> /etc/clamav/clamd.conf \

COPY entrypoint.sh /docker-entrypoint.sh 
RUN chmod +x /docker-entrypoint.sh

CMD [ "/docker-entrypoint.sh"]
#ENTRYPOINT [ "/usr/sbin/clamd", "-c", "/etc/clamav/clamd.conf" ]

EXPOSE 3310