"""Add persistent dashboard_cache_db table

Revision ID: 007
Revises: 006
Create Date: 2026-03-23 12:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '007'
down_revision: Union[str, None] = '006'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        'dashboard_cache_db',
        sa.Column('account_id', sa.Integer(),
                  sa.ForeignKey('accounts.id', ondelete='CASCADE'),
                  primary_key=True, nullable=False),
        sa.Column('colonies_json', sa.Text(), nullable=False, server_default='[]'),
        sa.Column('meta_json', sa.Text(), nullable=False, server_default='{}'),
        sa.Column('fetched_at', sa.DateTime(timezone=True), server_default=sa.func.now()),
    )


def downgrade() -> None:
    op.drop_table('dashboard_cache_db')
