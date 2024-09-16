FROM node:14
RUN groupadd -r appgroup && useradd -r -g appgroup appuser
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN chown -R appuser:appgroup /app
USER appuser
EXPOSE 3000
CMD ["node", "index.js"]
