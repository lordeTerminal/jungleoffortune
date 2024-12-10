import pyotp
import sys

# Chave secreta usada para gerar o TOTP
key = 'BAAZLEH5UGAWIHSGLXJZNN2GGB4XDWLF'
totp = pyotp.TOTP(key)

# Código TOTP digitado pelo usuário é passado como argumento
input_code = sys.argv[1]

# Verifica se o código é válido
if totp.verify(input_code):
    print("success")
else:
    print("failure")

