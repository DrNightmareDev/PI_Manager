"""Create kill_activity_cache table

Revision ID: 019_kill_activity_cache
Revises: 018_character_vacation_mode
Create Date: 2026-03-30
"""

from alembic import op
import sqlalchemy as sa

revision = "019_kill_activity_cache"
down_revision = "018_character_vacation_mode"
branch_labels = None
depends_on = None


def upgrade():
    op.create_table(
        "kill_activity_cache",
        sa.Column("system_id", sa.BigInteger(), nullable=False),
        sa.Column("kill_count", sa.Integer(), nullable=False, server_default="0"),
        sa.Column("fetched_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.PrimaryKeyConstraint("system_id"),
    )


def downgrade():
    op.drop_table("kill_activity_cache")
