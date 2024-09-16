FROM node:14
WORKDIR /app
COPY package*.json ./
RUN npm install
RUN apt-get update \
  && apt-get install -y nginx \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*
COPY . .
EXPOSE 3000
CMD ["node", "index.js"]
