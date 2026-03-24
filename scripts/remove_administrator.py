#!/usr/bin/env python3
import argparse
import sys
from pathlib import Path


REPO_ROOT = Path(__file__).resolve().parents[1]
if str(REPO_ROOT) not in sys.path:
    sys.path.insert(0, str(REPO_ROOT))

from app.database import SessionLocal  # noqa: E402
from app.models import Account, Character  # noqa: E402


def find_character(db, name: str | None, eve_id: int | None) -> Character | None:
    if eve_id is not None:
        return db.query(Character).filter(Character.eve_character_id == eve_id).first()
    if name:
        return db.query(Character).filter(Character.character_name.ilike(name)).first()
    return None


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Entfernt einem bestehenden Account per Charaktername oder EVE-ID die Administrator-Rechte."
    )
    group = parser.add_mutually_exclusive_group(required=True)
    group.add_argument("--name", help="Exakter Charaktername")
    group.add_argument("--eve-id", type=int, help="EVE Character ID")
    args = parser.parse_args()

    db = SessionLocal()
    try:
        character = find_character(db, args.name, args.eve_id)
        if not character:
            needle = args.name if args.name else args.eve_id
            print(f"Kein Charakter gefunden fuer: {needle}", file=sys.stderr)
            return 1

        account = db.query(Account).filter(Account.id == character.account_id).first()
        if not account:
            print(f"Kein Account fuer Charakter {character.character_name} gefunden.", file=sys.stderr)
            return 1

        account.is_owner = False
        account.is_admin = False
        db.commit()

        print("Administrator entfernt:")
        print(f"  Charakter: {character.character_name} ({character.eve_character_id})")
        print(f"  Account:   {account.id}")
        print(f"  Owner:     {account.is_owner}")
        print(f"  Manager:   {account.is_admin}")
        return 0
    finally:
        db.close()


if __name__ == "__main__":
    raise SystemExit(main())
