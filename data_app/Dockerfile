WORKDIR /app
COPY package*.json ./
RUN npm install
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y tzdata && \
    ln -fs /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && \
    dpkg-reconfigure --frontend noninteractive tzdata && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*
COPY . .
EXPOSE 3000
CMD ["node", "index.js"]
