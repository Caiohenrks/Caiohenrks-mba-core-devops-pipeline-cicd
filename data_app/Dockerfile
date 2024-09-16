FROM node:14
WORKDIR /app
COPY package*.json ./
RUN npm install
RUN apt-get install -y tzdata && \
    ln -fs /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && \
    dpkg-reconfigure --frontend noninteractive tzdata && \
    apt-get update && apt-get upgrade -y
COPY . .
EXPOSE 3000
CMD ["node", "index.js"]
