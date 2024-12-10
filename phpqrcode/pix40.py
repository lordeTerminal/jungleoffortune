# Importando o módulo
import sys
from pixqrcodegen import Payload

#nome = sys.argv[1]
#chavepix = sys.argv[2]
#valor = sys.argv[3]
#cidade = sys.argv[4]
#txtId = sys.argv[5]

# Parâmetros necessários
#payload = Payload(nome, chavepix, valor, cidade, txtId)

# Chamando a função responsável para gerar a Payload Pix e o QR Code
#payload.gerarPayload()

#####################################################################


payload = Payload('KevinVanBerghem', '41164508806', '39.99', 'Barueri', 'apresentacaoFATEC')


payload.gerarPayload()
