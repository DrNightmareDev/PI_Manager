"""Add skyhook_value_cache table

Revision ID: 008_skyhook_value_cache
Revises: 007_dashboard_cache
Create Date: 2026-03-23
"""

from alembic import op
import sqlalchemy as sa


revision = "008_skyhook_value_cache"
down_revision = "007"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "skyhook_value_cache",
        sa.Column("account_id", sa.Integer(), nullable=False),
        sa.Column("planet_id", sa.Integer(), nullable=False),
        sa.Column("price_mode", sa.String(length=10), nullable=False),
        sa.Column("total_value", sa.String(length=50), nullable=False, server_default="0"),
        sa.Column("details_json", sa.Text(), nullable=False, server_default="[]"),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.ForeignKeyConstraint(["account_id"], ["accounts.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("account_id", "planet_id", "price_mode"),
    )


def downgrade() -> None:
    op.drop_table("skyhook_value_cache")
