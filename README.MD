
# Geral

##  Implementação DevOps com Jenkins, SonarQube, Trivy, Nexus e Newman

Este projeto descreve a implementação de um pipeline DevOps utilizando as ferramentas Jenkins, SonarQube, Nexus e Newman. Essa configuração é ideal para ambientes de testes e experimentações, não sendo recomendada para produção devido a limitações na persistência de dados e questões de segurança das credenciais.

## Arquitetura e Configuração
O Jenkins, atuando como orquestrador do pipeline, é configurado para realizar a integração contínua (CI) e a entrega contínua (CD). Em um dos estágios do pipeline, o código fonte é analisado pelo SonarQube, uma ferramenta de análise de qualidade de código que ajuda a identificar possíveis vulnerabilidades e problemas de qualidade.

No entanto, as credenciais utilizadas para acessar o SonarQube são propositalmente simples e podem ser facilmente comprometidas. Essa escolha foi feita para simplificar a configuração inicial, mas não deve ser replicada em ambientes de produção.

Em seguida, o artefato gerado é armazenado no Nexus, um repositório de artefatos que facilita a gestão e distribuição de componentes. Similar ao Jenkins e ao SonarQube, o Nexus é configurado de forma básica, sem persistência de dados adequada, o que significa que qualquer dado armazenado será perdido em caso de reinicialização ou falhas.

Por fim, o Newman, um executor de coleções do Postman, é utilizado para rodar testes automatizados em APIs, garantindo que a aplicação está funcionando conforme esperado.

## Limitações e Riscos
É importante destacar que essa implementação não persiste dados entre reinicializações, o que pode resultar em perda de histórico e configurações caso o ambiente seja desligado. Além disso, o uso de credenciais fracas no SonarQube representa um risco de segurança que não deve ser ignorado.
# Getting Started

1. Efetuar o clone: `git clone https://github.com/Caiohenrks/mba-core-devops-pipeline-cicd.git .`.

2. Iniciar as VMs utilizando o Vagrant: `vagrant up jenkins sonarqube nexus`.

### Jenkins:  
- Acessar: http://192.168.50.10:8080
- Conectar na VM:   
`vagrant ssh jenkins`  
- Capturar a senha inicial para instalaçao:  
`sudo cat /var/lib/jenkins/secrets/initialAdminPassword`

### Sonarqube:  
- Acessar: http://192.168.50.20:9000
- Logar com usuário `admin` e senha `admin`.
- Criar uma nova senha.
- Criar um token http://192.168.50.20:9000/account/security e SALVE nas credenciais do Jenkins como **secret text** com o ID `SONARQUBE`.

### Jenkins:  
- Acessar: http://192.168.50.30:8081
- Conectar na VM:   
`vagrant ssh nexus`  
- Capturar a senha inicial para instalaçao:  
`docker exec -it nexus sh -c "cat /nexus-data/admin.password"` 
- Crie uma nova para admin e SALVE nas credencias do Jenkins com nome ID `NEXUS_LOGIN`, usuário `admin` senha `sua_senha`.


Após finalizar as configurações, acesse a VM do Jenkins e faça o restart dos dois serviços abaixo:
- Para aplicar as configurações do __/etc/docker/daemon.json__:  
`sudo systemctl restart docker`
-  Para aplicar as configuraçoes do __usermod -aG docker jenkins__:  
`sudo systemctl restart jenkins`


# Troubleshooting

### Problemas para conectar via SSH no windows
####  Jenkins
```ssh
cd "D:\Laboratórios\Core DevOps, Toolchain e Emerging\.vagrant\machines\jenkins\virtualbox"

icacls private_key /inheritance:r
icacls private_key /grant:r %username%:F

cd "D:\Laboratórios\Core DevOps, Toolchain e Emerging"

ssh -i .vagrant/machines/jenkins/virtualbox/private_key vagrant@192.168.50.10
```
#### Sonarqube
```ssh
cd "D:\Laboratórios\Core DevOps, Toolchain e Emerging\.vagrant\machines\sonarqube\virtualbox"

icacls private_key /inheritance:r
icacls private_key /grant:r %username%:F

cd "D:\Laboratórios\Core DevOps, Toolchain e Emerging"

ssh -i .vagrant/machines/sonarqube/virtualbox/private_key vagrant@192.168.50.20
```
#### Nexus
```ssh
cd "D:\Laboratórios\Core DevOps, Toolchain e Emerging\.vagrant\machines\nexus\virtualbox"

icacls private_key /inheritance:r
icacls private_key /grant:r %username%:F

cd "D:\Laboratórios\Core DevOps, Toolchain e Emerging"

ssh -i .vagrant/machines/nexus/virtualbox/private_key vagrant@192.168.50.30
```

