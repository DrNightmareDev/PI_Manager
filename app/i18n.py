import json
from functools import lru_cache
from pathlib import Path

from jinja2 import pass_context

LOCALES_DIR = Path(__file__).resolve().parent / "locales"
DEFAULT_LANGUAGE = "de"
SUPPORTED_LANGUAGES = ("de", "en", "zh-Hans")


@lru_cache(maxsize=1)
def load_translations() -> dict[str, dict[str, str]]:
    catalogs: dict[str, dict[str, str]] = {}
    for lang in SUPPORTED_LANGUAGES:
        path = LOCALES_DIR / f"{lang}.json"
        with path.open("r", encoding="utf-8") as handle:
            catalogs[lang] = json.load(handle)
    return catalogs


def clear_translation_cache() -> None:
    load_translations.cache_clear()


def normalize_language(value: str | None) -> str:
    if not value:
        return DEFAULT_LANGUAGE
    value = value.strip()
    if value in SUPPORTED_LANGUAGES:
        return value
    if value.startswith("zh"):
        return "zh-Hans"
    if value.startswith("en"):
        return "en"
    return DEFAULT_LANGUAGE


def get_language_from_request(request) -> str:
    if request is None:
        return DEFAULT_LANGUAGE
    cookie_lang = normalize_language(request.cookies.get("eve_lang"))
    if cookie_lang:
        return cookie_lang
    header = request.headers.get("accept-language", "")
    for part in header.split(","):
        lang = normalize_language(part.split(";")[0])
        if lang in SUPPORTED_LANGUAGES:
            return lang
    return DEFAULT_LANGUAGE


def translate(key: str, lang: str | None = None, **params) -> str:
    catalogs = load_translations()
    current_lang = normalize_language(lang)
    catalog = catalogs.get(current_lang, {})
    text = catalog.get(key) or catalogs[DEFAULT_LANGUAGE].get(key) or key
    if params:
        try:
            return text.format(**params)
        except Exception:
            return text
    return text


def get_client_catalog(lang: str | None = None) -> dict[str, str]:
    current_lang = normalize_language(lang)
    catalogs = load_translations()
    merged = dict(catalogs[DEFAULT_LANGUAGE])
    merged.update(catalogs.get(current_lang, {}))
    return merged


def get_translation_rows() -> list[dict[str, str]]:
    catalogs = load_translations()
    keys = sorted({key for catalog in catalogs.values() for key in catalog.keys()})
    rows: list[dict[str, str]] = []
    for key in keys:
        rows.append({
            "key": key,
            "en": catalogs.get("en", {}).get(key, ""),
            "de": catalogs.get("de", {}).get(key, ""),
            "zh-Hans": catalogs.get("zh-Hans", {}).get(key, ""),
        })
    return rows


def save_translation(locale: str, key: str, value: str) -> None:
    current_lang = normalize_language(locale)
    path = LOCALES_DIR / f"{current_lang}.json"
    with path.open("r", encoding="utf-8") as handle:
        catalog = json.load(handle)
    catalog[key] = value
    ordered = dict(sorted(catalog.items(), key=lambda item: item[0]))
    with path.open("w", encoding="utf-8") as handle:
        json.dump(ordered, handle, ensure_ascii=False, indent=2)
        handle.write("\n")
    clear_translation_cache()


@pass_context
def t(context, key: str, **params) -> str:
    request = context.get("request")
    lang = get_language_from_request(request)
    return translate(key, lang, **params)


@pass_context
def current_lang(context) -> str:
    request = context.get("request")
    return get_language_from_request(request)


@pass_context
def client_i18n(context) -> dict[str, str]:
    request = context.get("request")
    return get_client_catalog(get_language_from_request(request))
