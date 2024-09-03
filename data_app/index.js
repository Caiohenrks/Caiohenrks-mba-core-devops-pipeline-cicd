const express = require('express');
const qr = require('qr-image');
const redis = require('redis');
const bodyParser = require('body-parser');

const app = express();
const port = 3000;

const client = redis.createClient({
  host: 'redis',
  port: 6379
});

app.use(bodyParser.json());

app.post('/generate', (req, res) => {
  const text = req.body.text;
  if (!text) {
    return res.status(400).send('Texto é necessário');
  }

  const qr_svg = qr.imageSync(text, { type: 'png' });

  const key = `qr:${Date.now()}`;
  client.setex(key, 60, qr_svg.toString('base64'), (err) => { 
    if (err) {
      return res.status(500).send('Erro ao salvar QR Code no Redis');
    }

    res.json({ key });
  });
});

app.get('/retrieve/:key', (req, res) => {
  const key = req.params.key;

  client.get(key, (err, data) => {
    if (err || !data) {
      return res.status(404).send('QR Code não encontrado ou expirado');
    }

    res.json({ image: data });
  });
});

app.listen(port, () => {
  console.log(`Servidor rodando na porta ${port}`);
});
