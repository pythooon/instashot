defmodule PhoenixApiWeb.Plugs.ImportRateLimit do
  import Plug.Conn
  import Phoenix.Controller, only: [json: 2]

  def init(opts), do: opts

  def call(conn, _opts) do
    user = conn.assigns.current_user

    case PhoenixApi.ImportRateLimiter.reserve(user.id) do
      :ok ->
        conn

      {:error, :user_rate_limited} ->
        reject(conn, "Too many photo list requests for this account. Try again in a few minutes.")

      {:error, :global_rate_limited} ->
        reject(conn, "Photo list service is temporarily overloaded. Try again later.")
    end
  end

  defp reject(conn, message) do
    conn
    |> put_status(:too_many_requests)
    |> json(%{errors: %{detail: message}})
    |> halt()
  end
end
