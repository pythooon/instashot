defmodule PhoenixApiWeb.ConnCase do
  use ExUnit.CaseTemplate

  import Phoenix.ConnTest

  using do
    quote do
      import Plug.Conn
      import Phoenix.ConnTest

      @endpoint PhoenixApiWeb.Endpoint
    end
  end

  setup tags do
    pid = Ecto.Adapters.SQL.Sandbox.start_owner!(PhoenixApi.Repo, shared: not tags[:async])
    on_exit(fn -> Ecto.Adapters.SQL.Sandbox.stop_owner(pid) end)
    {:ok, conn: build_conn()}
  end
end
