PACKAGE_FILE="/home/vagrant/packages.ok"

echo "America/Sao_Paulo" | sudo tee /etc/timezone
sudo dpkg-reconfigure --frontend noninteractive tzdata

if [ ! -f "$PACKAGE_FILE" ]
then
    sudo sed -i -e "/http:\/\/archive.ubuntu.com/ s/archive.ubuntu.com/ubuntu.c3sl.ufpr.br/" /etc/apt/sources.list
    sudo aptitude update -y
    sudo aptitude upgrade -y
    sudo locale-gen en_US en_US.UTF-8 pt_BR pt_BR.UTF-8
    sudo dpkg-reconfigure locales
    sudo aptitude install php5 php5-cli php5-intl php5-curl -y

    #install elasticsearch
    sudo aptitude install openjdk-7-jre-headless -y
    wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.1.1.deb
    sudo dpkg -i elasticsearch-1.1.1.deb
    sudo service elasticsearch start

    sudo a2enmod rewrite
    sed -i '/$\tAllowOverride None/c AllowOverride All' /etc/apache2/apache2.conf
    sudo service apache2 restart

    touch $PACKAGE_FILE
fi

if [ ! -f "/usr/local/bin/composer.phar" ]
then
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/bin
    [ -f "/usr/bin/cpz" ] || sudo ln -s /usr/bin/composer.phar /usr/bin/cpz
    [ -f "/usr/bin/composer" ] || sudo ln -s /usr/bin/composer.phar /usr/bin/composer
else
    sudo /usr/bin/cpz self-update
fi

cd /var/www

rm html/index.htm

#install project dependencies
composer install

#create elasticsearch index
curl -XPUT 'localhost:9200/solicitacoes?pretty'

curl -XPUT -d '{"properties":{"assunto":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"bairro":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"canal":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"cep":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"timestamp":{"type":"date","format":"dateOptionalTime"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"conclusao":{"type":"date","format":"dateOptionalTime"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"endereco":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"especificacao":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"orgao":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"pag_guia":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"referencia":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"setor_quadra":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"situacao":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"subprefeitura":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'
curl -XPUT -d '{"properties":{"supervisao":{"type":"string","index":"not_analyzed"}}}' 'localhost:9200/solicitacoes/_mapping/solicitacao'

#start bot
nohup php /var/www/bot.php &