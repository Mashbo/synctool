FROM phusion/baseimage:0.9.18
ADD insecure_key.pub /tmp/insecure_key.pub
RUN rm -f /etc/service/sshd/down && cat /tmp/insecure_key.pub >> /root/.ssh/authorized_keys && rm -f /tmp/insecure_key.pub
RUN sudo apt-get update && sudo apt-get install -y mysql-server rsync
RUN sudo service mysql start && echo "CREATE DATABASE remote; USE remote; CREATE TABLE remote (name VARCHAR(20)); CREATE USER 'remote_db_user'@'localhost'; INSERT INTO remote values ('remote')" | mysql -u root
EXPOSE 22