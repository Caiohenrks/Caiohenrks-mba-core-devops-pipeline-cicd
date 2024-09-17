FROM node:14
RUN addgroup -S nonroot && adduser -S nonroot -G nonroot
USER nonroot
WORKDIR /app
COPY --chown=nonroot:nonroot package*.json ./
RUN npm install
COPY --chown=nonroot:nonroot . .
EXPOSE 3000
CMD ["node", "index.js"]

