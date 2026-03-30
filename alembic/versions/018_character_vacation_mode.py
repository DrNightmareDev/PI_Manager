"""Add vacation_mode to characters

Revision ID: 018_character_vacation_mode
Revises: 017_sso_state_created_at_index
Create Date: 2026-03-30
"""

from alembic import op
import sqlalchemy as sa

revision = "018_character_vacation_mode"
down_revision = "017_sso_state_created_at_index"
branch_labels = None
depends_on = None


def upgrade():
    op.add_column(
        "characters",
        sa.Column("vacation_mode", sa.Boolean(), nullable=False, server_default=sa.text("false")),
    )


def downgrade():
    op.drop_column("characters", "vacation_mode")
