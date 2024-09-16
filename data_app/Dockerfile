WORKDIR /app
COPY package*.json ./
RUN npm install
RUN apt-get update && apt-get upgrade -y
COPY . .
EXPOSE 3000
CMD ["node", "index.js"]
