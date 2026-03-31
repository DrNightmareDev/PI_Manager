"""add soft delete to inventory transactions

Revision ID: 029_inventory_tx_soft_delete
Revises: 028_inventory_soft_delete
Create Date: 2026-03-31
"""

from alembic import op
import sqlalchemy as sa


revision = "029_inventory_tx_soft_delete"
down_revision = "028_inventory_soft_delete"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.add_column("inventory_lots", sa.Column("deleted_at", sa.DateTime(timezone=True), nullable=True))
    op.add_column("inventory_adjustments", sa.Column("deleted_at", sa.DateTime(timezone=True), nullable=True))
    op.create_index("ix_inventory_lots_deleted_at", "inventory_lots", ["deleted_at"], unique=False)
    op.create_index("ix_inventory_adjustments_deleted_at", "inventory_adjustments", ["deleted_at"], unique=False)


def downgrade() -> None:
    op.drop_index("ix_inventory_adjustments_deleted_at", table_name="inventory_adjustments")
    op.drop_index("ix_inventory_lots_deleted_at", table_name="inventory_lots")
    op.drop_column("inventory_adjustments", "deleted_at")
    op.drop_column("inventory_lots", "deleted_at")
