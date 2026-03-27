"""planet templates

Revision ID: 013_planet_templates
Revises: 012_character_colony_sync_status
Create Date: 2026-03-27 10:00:00
"""

from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = "013_planet_templates"
down_revision = "012_character_colony_sync_status"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "planet_templates",
        sa.Column("id", sa.Integer(), primary_key=True, index=True),
        sa.Column("account_id", sa.Integer(), sa.ForeignKey("accounts.id", ondelete="CASCADE"), nullable=True, index=True),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("description", sa.Text(), nullable=True),
        sa.Column("planet_type", sa.String(64), nullable=True),
        sa.Column("layout_json", sa.Text(), nullable=False),
        sa.Column("is_community", sa.Boolean(), nullable=False, server_default=sa.false()),
        sa.Column("source_url", sa.String(512), nullable=True),
        sa.Column("created_at", sa.DateTime(timezone=True), server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), onupdate=sa.func.now()),
    )


def downgrade() -> None:
    op.drop_table("planet_templates")
