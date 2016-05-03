FROM phusion/baseimage:0.9.18
ADD insecure_key.pub /tmp/insecure_key.pub
ADD insecure_key /tmp/insecure_key
ADD assert-copy-succeeded.sh /tmp/assert-copy-succeeded.sh
RUN cat /tmp/insecure_key.pub > /root/.ssh/id_rsa.pub && cat /tmp/insecure_key > ~/.ssh/id_rsa && chmod -R 600 ~/.ssh/id_rsa*
RUN sudo apt-get update && sudo apt-get install -y mysql-server php5 rsync
RUN sudo service mysql start && echo "CREATE DATABASE local;" | mysql -u root