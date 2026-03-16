# 📸 Snap CMS
**Jednoduchý, file-based CMS s inline editováním pro PHP.**

Snap CMS je jednoduchý content management systém pro menší weby. Umožňuje jednoduše editovat texty přímo v prohlížeči.

---

## ✨ Proč Snap CMS?
* **Žádná databáze:** Jednoduše file-based
* **Inline editace:** Upravuj texty přímo v kontextu webu.
* **Lehkost:** Minimum kódu, žádné těžké knihovny.

## 🚀 Jak to rozjet?

### 1. Instalace
Stačí nahrát soubory na server. 
> [!IMPORTANT]
> Soubor `.htaccess` musí zůstat v kořenové složce. Je nezbytný pro správné směrování na cestu `/login`.

### 2. Přihlášení
Administrace je defaultně dostupná na adrese `tvujweb.cz/login`.
* **Výchozí heslo:** `heslo`
> [!TIP]
> Adresa pro přihlášení i odhlášení se dá změnit v `db.json`

#### Změna hesla
Změna hesla zatím není dostupná v uživatelském rozhraní. Dočasně jde změnit jen v PHP přes
```php
$cms->setPassword("mojeNoveHeslo1234");
```

## 🛠 Použití na webu

Jakýkoliv editovatelný text se na web vkládá přes
```php
$cms->text('id-textu');
```
CMS automaticky na web vloží odstavec Lorem ipsum. Pro kratší texty je vhodnější
```php
$cms->text('id-textu', $cms::SHORT);
```

To je vše :D
