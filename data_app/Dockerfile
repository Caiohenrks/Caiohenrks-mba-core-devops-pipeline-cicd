FROM node:14
WORKDIR /app
COPY package*.json ./
RUN npm install --ignore-scripts
COPY index.js .
COPY index.php .
EXPOSE 3000
CMD ["node", "index.js"]

