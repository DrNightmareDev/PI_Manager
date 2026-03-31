"""add hauling preferences

Revision ID: 030_hauling_preferences
Revises: 029_inventory_tx_soft_delete
Create Date: 2026-03-31
"""

from alembic import op
import sqlalchemy as sa


revision = "030_hauling_preferences"
down_revision = "029_inventory_tx_soft_delete"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "hauling_preferences",
        sa.Column("account_id", sa.Integer(), nullable=False),
        sa.Column("return_to_start", sa.Boolean(), nullable=False, server_default=sa.text("false")),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.ForeignKeyConstraint(["account_id"], ["accounts.id"], ondelete="CASCADE"),
        sa.PrimaryKeyConstraint("account_id"),
    )


def downgrade() -> None:
    op.drop_table("hauling_preferences")
