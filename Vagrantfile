Vagrant.configure("2") do |config|
  config.vm.define "jenkins" do |jenkins_config|
    jenkins_config.vm.box = "generic/oracle9"
    jenkins_config.vm.network "forwarded_port", guest: 8080, host: 8080
    jenkins_config.vm.network "private_network", ip: "192.168.50.10"

    jenkins_config.vm.provider "virtualbox" do |vb|
      vb.name = "Jenkins"
      vb.memory = "2048"
      vb.cpus = 2
      vb.customize ["modifyvm", :id, "--uartmode1", "disconnected"]
    end

    jenkins_config.vm.provision "shell", inline: <<-SHELL
      sudo dnf install -y epel-release
      sudo dnf update -y
      sudo dnf install -y dnf-plugins-core
      sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
      sudo dnf install -y docker-ce docker-ce-cli containerd.io git
      sudo systemctl start docker
      sudo systemctl enable docker
      sudo usermod -aG docker vagrant
      sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
      sudo chmod +x /usr/local/bin/docker-compose

      sudo wget -O /etc/yum.repos.d/jenkins.repo \
          https://pkg.jenkins.io/redhat-stable/jenkins.repo
      sudo rpm --import https://pkg.jenkins.io/redhat-stable/jenkins.io-2023.key
      sudo yum upgrade
      sudo yum install fontconfig java-17-openjdk -y
      sudo yum install jenkins -y
      sudo systemctl daemon-reload
      sudo systemctl enable jenkins
      sudo systemctl start jenkins

      sudo usermod -aG docker jenkins

      sudo firewall-cmd --zone=public --add-port=8080/tcp --permanent
      sudo firewall-cmd --reload

      sudo echo '{ "insecure-registries" : ["192.168.50.30:8082"] }' | sudo tee /etc/docker/daemon.json
      sudo systemctl restart docker

    SHELL

    #jenkins_config.vm.synced_folder "./data_jenkins", "/var/lib/jenkins", type: "rsync", rsync__auto: true
  end
  
  config.vm.define "sonarqube" do |sonarqube_config|
    sonarqube_config.vm.box = "generic/oracle9"
    sonarqube_config.vm.network "forwarded_port", guest: 9000, host: 9000
    sonarqube_config.vm.network "private_network", ip: "192.168.50.20"

    sonarqube_config.vm.provider "virtualbox" do |vb|
      vb.name = "Sonarqube"
      vb.memory = "4098"
      vb.cpus = 2
      vb.customize ["modifyvm", :id, "--uartmode1", "disconnected"]
    end

    sonarqube_config.vm.provision "shell", inline: <<-SHELL
      sudo sysctl -w vm.max_map_count=262144
      echo "vm.max_map_count=262144" | sudo tee -a /etc/sysctl.conf

      sudo dnf install -y dnf-plugins-core
      sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
      sudo dnf install -y docker-ce docker-ce-cli containerd.io
      sudo systemctl start docker
      sudo systemctl enable docker
      sudo usermod -aG docker vagrant
      
      sudo docker network create sonarqube-network

      sudo docker run -d --name sonarqube-db \
      --network sonarqube-network \
      -e POSTGRES_USER=sonar \
      -e POSTGRES_PASSWORD=sonarpassword \
      -e POSTGRES_DB=sonarqube \
      -v sonarqube_db:/var/lib/postgresql/data \
      --restart always \
      postgres:latest

      sudo docker run -d --name sonarqube \
      --network sonarqube-network \
      -p 9000:9000 \
      -e SONAR_JDBC_URL=jdbc:postgresql://sonarqube-db:5432/sonarqube \
      -e SONAR_JDBC_USERNAME=sonar \
      -e SONAR_JDBC_PASSWORD=sonarpassword \
      -v sonarqube_data:/opt/sonarqube/data \
      -v sonarqube_extensions:/opt/sonarqube/extensions \
      -v sonarqube_logs:/opt/sonarqube/logs \
      --restart always \
      sonarqube:latest

      sudo firewall-cmd --zone=public --add-port=9000/tcp --permanent
      sudo firewall-cmd --reload
    SHELL
    #sonarqube_config.vm.synced_folder "./data_sonarqube", "/data", type: "virtualbox"
  end

  # Configuração para o Nexus
  config.vm.define "nexus" do |nexus_config|
    nexus_config.vm.box = "generic/oracle9"
    nexus_config.vm.network "forwarded_port", guest: 8081, host: 8081
    nexus_config.vm.network "forwarded_port", guest: 8082, host: 8082
    nexus_config.vm.network "forwarded_port", guest: 8083, host: 8083
    nexus_config.vm.network "private_network", ip: "192.168.50.30"

    nexus_config.vm.provider "virtualbox" do |vb|
      vb.name = "Nexus"
      vb.memory = "4098"
      vb.cpus = 2
      vb.customize ["modifyvm", :id, "--uartmode1", "disconnected"]
    end

    nexus_config.vm.provision "shell", inline: <<-SHELL
      sudo sysctl -w vm.max_map_count=262144
      echo "vm.max_map_count=262144" | sudo tee -a /etc/sysctl.conf

      sudo dnf install -y dnf-plugins-core
      sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
      sudo dnf install -y docker-ce docker-ce-cli containerd.io
      sudo systemctl start docker
      sudo systemctl enable docker
      sudo usermod -aG docker vagrant

      sudo docker run -d -p 8081:8081 -p 8082:8082 -p 8083:8083 --restart always -v nexus-data:/nexus-data --name nexus sonatype/nexus3 

      sudo firewall-cmd --zone=public --add-port=8081/tcp --permanent
      sudo firewall-cmd --zone=public --add-port=8082/tcp --permanent
      sudo firewall-cmd --zone=public --add-port=8083/tcp --permanent
      sudo firewall-cmd --reload
    SHELL
    #nexus_config.vm.synced_folder "./data_nexus", "/data", type: "virtualbox"
  end 
end
