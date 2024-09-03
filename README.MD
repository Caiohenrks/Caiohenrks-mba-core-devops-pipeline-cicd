


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

