"""add market avg_volume_7d

Revision ID: 009_market_volume_7d
Revises: 008_skyhook_value_cache
Create Date: 2026-03-24
"""

from alembic import op
import sqlalchemy as sa


revision = "009_market_volume_7d"
down_revision = "008_skyhook_value_cache"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.add_column("market_cache", sa.Column("avg_volume_7d", sa.String(length=50), nullable=True))


def downgrade() -> None:
    op.drop_column("market_cache", "avg_volume_7d")
