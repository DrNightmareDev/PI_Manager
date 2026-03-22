"""Add is_owner to accounts

Revision ID: 003
Revises: 002
Create Date: 2026-03-22 00:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '003'
down_revision: Union[str, None] = '002'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.add_column('accounts', sa.Column('is_owner', sa.Boolean(), nullable=False, server_default='false'))


def downgrade() -> None:
    op.drop_column('accounts', 'is_owner')
