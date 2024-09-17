FROM node:14

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN addgroup -S nonroot \
    && adduser -S nonroot -G nonroot
USER nonroot
EXPOSE 3000
CMD ["node", "index.js"]
