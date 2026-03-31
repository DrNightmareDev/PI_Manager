"""add combat intel preferences

Revision ID: 025_combat_intel_preferences
Revises: 024_intel_kill_events
Create Date: 2026-03-31 20:05:00.000000
"""

from alembic import op
import sqlalchemy as sa


revision = "025_combat_intel_preferences"
down_revision = "024_intel_kill_events"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "combat_intel_preferences",
        sa.Column("account_id", sa.Integer(), sa.ForeignKey("accounts.id", ondelete="CASCADE"), primary_key=True),
        sa.Column("region_id", sa.BigInteger(), nullable=True),
        sa.Column("window", sa.String(length=10), nullable=False, server_default="60m"),
        sa.Column("kill_type", sa.String(length=20), nullable=False, server_default="all"),
        sa.Column("layout", sa.String(length=10), nullable=False, server_default="geo"),
        sa.Column("tracked_character_id", sa.Integer(), sa.ForeignKey("characters.id", ondelete="SET NULL"), nullable=True),
        sa.Column("follow_character", sa.Boolean(), nullable=False, server_default=sa.false()),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
    )


def downgrade() -> None:
    op.drop_table("combat_intel_preferences")
