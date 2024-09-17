const request = require('supertest');
const chai = require('chai');
const sinon = require('sinon');
const redis = require('redis');
const app = require('./index'); // Certifique-se de exportar o app do seu index.js
const { expect } = chai;

describe('QR Code API', () => {
  let client;

  before(() => {
    // Cria um stub para o cliente Redis
    client = sinon.stub(redis, 'createClient').returns({
      setex: sinon.stub().yields(null),
      get: sinon.stub().yields(null, 'base64encodeddata')
    });
  });

  after(() => {
    // Restaurar o stub após os testes
    client.restore();
  });

  it('should generate QR code and return key', async () => {
    const response = await request(app)
      .post('/generate')
      .send({ text: 'Hello World' })
      .expect(200);

    expect(response.body).to.have.property('key');
  });

  it('should return QR code image by key', async () => {
    const response = await request(app)
      .get('/retrieve/qr:123456789')
      .expect(200);

    expect(response.body).to.have.property('image');
  });

  it('should return 400 if text is missing', async () => {
    await request(app)
      .post('/generate')
      .send({})
      .expect(400);
  });

  it('should return 404 if QR code is not found', async () => {
    // Modifica o stub para retornar erro
    client.restore();
    sinon.stub(redis, 'createClient').returns({
      get: sinon.stub().yields(new Error('Not Found'), null)
    });

    await request(app)
      .get('/retrieve/invalidkey')
      .expect(404);
  });
});
