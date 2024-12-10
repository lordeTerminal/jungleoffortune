#!/usr/bin/env python3

import time
import pyotp

#key = pyotp.random_base32()
key = 'BAAZLEH5UGAWIHSGLXJZNN2GGB4XDWLF'

#print(key)
totp = pyotp.TOTP(key)

print(totp.now())

input_code = input("Entre com dois fatores: ")

print(totp.verify(input_code))

