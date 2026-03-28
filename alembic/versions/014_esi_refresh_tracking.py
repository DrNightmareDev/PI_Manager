"""ESI refresh tracking + planet ETag cache

Revision ID: 014_esi_refresh_tracking
Revises: 013_planet_templates
Create Date: 2026-03-28
"""

from alembic import op
import sqlalchemy as sa

revision = "014_esi_refresh_tracking"
down_revision = "013_planet_templates"
branch_labels = None
depends_on = None


def upgrade() -> None:
    # Per-character ESI refresh tracking
    op.add_column("characters", sa.Column(
        "last_esi_refresh_at", sa.DateTime(timezone=True), nullable=True
    ))
    op.add_column("characters", sa.Column(
        "esi_consecutive_errors", sa.Integer(), nullable=False, server_default="0"
    ))

    # Per-planet ETag cache — avoids re-fetching unchanged planets
    op.create_table(
        "planet_esi_cache",
        sa.Column("id", sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column("eve_character_id", sa.BigInteger(), nullable=False),
        sa.Column("planet_id", sa.Integer(), nullable=False),
        sa.Column("etag", sa.String(255), nullable=True),
        sa.Column("response_json", sa.Text(), nullable=False, server_default="{}"),
        sa.Column("fetched_at", sa.DateTime(timezone=True), server_default=sa.func.now()),
        sa.UniqueConstraint("eve_character_id", "planet_id", name="uq_planet_esi_cache"),
    )
    op.create_index(
        "ix_planet_esi_cache_char_planet",
        "planet_esi_cache",
        ["eve_character_id", "planet_id"],
    )


def downgrade() -> None:
    op.drop_index("ix_planet_esi_cache_char_planet", table_name="planet_esi_cache")
    op.drop_table("planet_esi_cache")
    op.drop_column("characters", "esi_consecutive_errors")
    op.drop_column("characters", "last_esi_refresh_at")
