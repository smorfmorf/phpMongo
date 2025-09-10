# def fix_encoding(garbled_text):
#     # Преобразуем исходную строку в байты (ошибочно интерпретированные Windows-1252 символы)
#     raw_bytes = garbled_text.encode('latin1')  # 'latin1' = Windows-1252 без ошибок
#     # Читаем эти байты как Windows-1251
#     fixed_text = raw_bytes.decode('windows-1251')
#     return fixed_text

# # Пример:
# garbled = "ðïäô÷åòöäåîéå: óïïâýåîéå ãõîáíé ÷åðá56 ðôãã 240000 îïíåò 013 ðïìõþåîï ÷ 000126Z 2406 AUTO RUHB "

# fixed = fix_encoding(garbled)
# print(f"Результат: {fixed}")

#!

# # строка-кракозябра
# s = "Ð\x9fÑ\x80Ð¸Ð²ÐµÑ\x82"

# # 1. Получаем байты из строки, считая, что она была неправильно прочитана как windows-1252
# b = s.encode('latin1')  # ⬅️ латин1 безопасно, 1 байт = 1 символ
# print(b)
# # 2. Декодируем байты как UTF-8 (как и надо было изначально)
# fixed = b.decode('utf-8')

# print(fixed)  # 👉 Привет

# ******

# import encodings
# import codecs

# # Кракозябра
# wtf = "÷åðá56ðôãã"

# # Список всех известных кодировок
# all_encodings = sorted(set(encodings.aliases.aliases.values()))

# for enc in all_encodings:
#     try:
#         # encode через latin1 (чтобы получить байты)
#         b = wtf.encode('latin1')

#         # decode через текущую кодировку
#         decoded = b.decode(enc)
#         print(f"[{enc}]: {decoded}")
#     except Exception:
#         # если кодировка не подходит, просто пропускаем
#         pass
# ******
import encodings
import pkgutil

# Собираем все стандартные кодировки из пакета encodings
all_encodings = set(
    [modname for _, modname, _ in pkgutil.iter_modules(encodings.__path__)]
)

# Добавляем алиасы, чтобы не упустить варианты
import encodings.aliases
all_encodings.update(encodings.aliases.aliases.values())

# Сортируем для удобного просмотра
all_encodings = sorted(all_encodings)

# Пример: вывод всех кодировок
for enc in all_encodings:
    print(enc)

# Пробегаем через все кодировки и декодируем кракозябру
wtf = "÷åðá56ðôãã"
for enc in all_encodings:
    try:
        # Сначала получаем байты из кракозябры через latin1
        b = wtf.encode('latin1')
        # Декодируем через текущую кодировку
        decoded = b.decode(enc)
        print(f"[{enc}]: {decoded}")
    except Exception:
        pass  # Игнорируем кодировки, которые не поддерживают этот байт
