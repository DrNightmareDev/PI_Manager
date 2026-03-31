"""add inventory stock tables

Revision ID: 027_inventory_stock
Revises: 026_intel_stream_state
Create Date: 2026-03-31 22:05:00.000000
"""

from alembic import op
import sqlalchemy as sa


revision = "027_inventory_stock"
down_revision = "026_intel_stream_state"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "inventory_item_summaries",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("account_id", sa.Integer(), sa.ForeignKey("accounts.id", ondelete="CASCADE"), nullable=False),
        sa.Column("type_id", sa.Integer(), nullable=False),
        sa.Column("item_name", sa.String(length=255), nullable=False),
        sa.Column("tier", sa.String(length=10), nullable=False),
        sa.Column("quantity_on_hand", sa.BigInteger(), nullable=False, server_default="0"),
        sa.Column("weighted_average_cost", sa.String(length=50), nullable=True),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.UniqueConstraint("account_id", "type_id", name="uq_inventory_item_account_type"),
    )
    op.create_index("ix_inventory_item_summaries_account_id", "inventory_item_summaries", ["account_id"])
    op.create_index("ix_inventory_item_summaries_type_id", "inventory_item_summaries", ["type_id"])
    op.create_index("ix_inventory_item_account_tier", "inventory_item_summaries", ["account_id", "tier"])

    op.create_table(
        "inventory_lots",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("account_id", sa.Integer(), sa.ForeignKey("accounts.id", ondelete="CASCADE"), nullable=False),
        sa.Column("type_id", sa.Integer(), nullable=False),
        sa.Column("item_name", sa.String(length=255), nullable=False),
        sa.Column("tier", sa.String(length=10), nullable=False),
        sa.Column("quantity_added", sa.BigInteger(), nullable=False),
        sa.Column("quantity_remaining", sa.BigInteger(), nullable=False),
        sa.Column("unit_cost", sa.String(length=50), nullable=True),
        sa.Column("total_cost", sa.String(length=50), nullable=True),
        sa.Column("source_kind", sa.String(length=20), nullable=False, server_default="manual"),
        sa.Column("note", sa.String(length=255), nullable=True),
        sa.Column("created_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
    )
    op.create_index("ix_inventory_lots_account_id", "inventory_lots", ["account_id"])
    op.create_index("ix_inventory_lots_type_id", "inventory_lots", ["type_id"])
    op.create_index("ix_inventory_lots_tier", "inventory_lots", ["tier"])
    op.create_index("ix_inventory_lots_created_at", "inventory_lots", ["created_at"])
    op.create_index("ix_inventory_lot_account_type_created", "inventory_lots", ["account_id", "type_id", "created_at"])

    op.create_table(
        "inventory_adjustments",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("account_id", sa.Integer(), sa.ForeignKey("accounts.id", ondelete="CASCADE"), nullable=False),
        sa.Column("type_id", sa.Integer(), nullable=False),
        sa.Column("item_name", sa.String(length=255), nullable=False),
        sa.Column("tier", sa.String(length=10), nullable=False),
        sa.Column("delta_quantity", sa.BigInteger(), nullable=False),
        sa.Column("reason", sa.String(length=50), nullable=False, server_default="manual"),
        sa.Column("note", sa.String(length=255), nullable=True),
        sa.Column("created_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
    )
    op.create_index("ix_inventory_adjustments_account_id", "inventory_adjustments", ["account_id"])
    op.create_index("ix_inventory_adjustments_type_id", "inventory_adjustments", ["type_id"])
    op.create_index("ix_inventory_adjustments_tier", "inventory_adjustments", ["tier"])
    op.create_index("ix_inventory_adjustments_created_at", "inventory_adjustments", ["created_at"])
    op.create_index("ix_inventory_adjustment_account_type_created", "inventory_adjustments", ["account_id", "type_id", "created_at"])


def downgrade() -> None:
    op.drop_index("ix_inventory_adjustment_account_type_created", table_name="inventory_adjustments")
    op.drop_index("ix_inventory_adjustments_created_at", table_name="inventory_adjustments")
    op.drop_index("ix_inventory_adjustments_tier", table_name="inventory_adjustments")
    op.drop_index("ix_inventory_adjustments_type_id", table_name="inventory_adjustments")
    op.drop_index("ix_inventory_adjustments_account_id", table_name="inventory_adjustments")
    op.drop_table("inventory_adjustments")

    op.drop_index("ix_inventory_lot_account_type_created", table_name="inventory_lots")
    op.drop_index("ix_inventory_lots_created_at", table_name="inventory_lots")
    op.drop_index("ix_inventory_lots_tier", table_name="inventory_lots")
    op.drop_index("ix_inventory_lots_type_id", table_name="inventory_lots")
    op.drop_index("ix_inventory_lots_account_id", table_name="inventory_lots")
    op.drop_table("inventory_lots")

    op.drop_index("ix_inventory_item_account_tier", table_name="inventory_item_summaries")
    op.drop_index("ix_inventory_item_summaries_type_id", table_name="inventory_item_summaries")
    op.drop_index("ix_inventory_item_summaries_account_id", table_name="inventory_item_summaries")
    op.drop_table("inventory_item_summaries")
