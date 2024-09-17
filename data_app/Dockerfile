FROM node:14
RUN addgroup -S nonroot && adduser -S nonroot -G nonroot
USER nonroot
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY index.js .
COPY index.php .
EXPOSE 3000
CMD ["node", "index.js"]

